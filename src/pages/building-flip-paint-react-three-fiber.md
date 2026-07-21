---
layout: ../layouts/Blog.astro
title: "Title 1"
subtitle: "Title 2 Title 2 Title 2"
# poster: "/images/darran-o-shea.jpg"
---

# Building Flip Paint in React Three Fiber: A Journey into `onBeforeCompile`

When I started building a configurable 3D product viewer, I thought
adding a flip paint (or chameleon paint) option would be relatively
straightforward.

Three.js already has `MeshPhysicalMaterial` with support for
iridescence, so surely it was just a case of enabling a few
properties...

It turns out, not quite.

## The Goal

I wanted to create a material that behaved like automotive flip paint:

- one colour when viewed head-on
- gradually shifting through multiple colours as the viewing angle
  changes
- retaining all of the realism of `MeshPhysicalMaterial`
- configurable with simple hex colours
- reusable as a React Three Fiber component

The important part was keeping all of the physical lighting,
reflections, clearcoat and environment mapping that Three.js already
provides.

Replacing the material with a custom `ShaderMaterial` wasn't really an
option.

![Finished flip paint](images/flip-paint-final.png)

## The First Attempt

My first approach was using Three.js's built-in iridescence.

```jsx
<meshPhysicalMaterial
  iridescence={1}
  iridescenceIOR={1.3}
  iridescenceThicknessRange={[200, 800]}
/>
```

While it looked nice, it didn't let me define specific colours.

Real flip paints often shift from something like:

- Purple → Blue → Green
- Gold → Orange → Red
- Cyan → Purple → Pink

I wanted artistic control rather than physically accurate thin-film
interference.

## Enter `onBeforeCompile`

The breakthrough came from extending `MeshPhysicalMaterial` rather than
replacing it.

Using `onBeforeCompile`, I injected my own uniforms and fragment shader
code into Three.js's existing material.

That meant I could keep:

- PBR lighting
- reflections
- clearcoat
- iridescence
- environment maps

while only replacing the colour calculation.

It's one of those features that's incredibly powerful once you discover
it.

## Building the Fresnel Effect

The colour shift is driven by a Fresnel calculation.

Rather than using the built-in iridescence colours, I calculate how
oblique the viewing angle is.

From there I blend between three colours.

```glsl
float fresnelRaw =
    1.0 -
    max(dot(normalize(vNormal),
            normalize(vViewPosition)), 0.0);

float fresnel = pow(fresnelRaw, uIntensity);
```

This value becomes the driver for the colour transitions.

Front-on? Use the base colour.

Halfway? Blend towards the second colour.

At grazing angles? Transition to the third colour.

## Making It Pop

The first version worked.

Technically.

Visually... it looked a little flat.

So I started experimenting.

### Dynamic Fresnel Boost

Instead of applying the same multiplier everywhere, I amplified the
effect towards the edges.

```glsl
float boost =
    mix(uFresnelBoost,
        uFresnelBoost * uFresnelEdgeBoost,
        fresnel);

fresnel = clamp(fresnel * boost, 0.0, 1.0);
```

This created much more dramatic flashes at shallow viewing angles.

Much closer to real automotive paint.

## The "Why Is Everything So Dark?" Moment

Then came the first head-scratcher.

My colours looked significantly darker than the hex values I was
supplying.

After a bit of investigation I realised I was effectively tinting an
already physically lit material.

The solution wasn't to crank up the lighting.

Instead I changed the final blend.

Originally I was multiplying.

```glsl
gl_FragColor.rgb *= flipColor;
```

Instead I blended the colours over the physical shading.

```glsl
gl_FragColor.rgb =
    mix(gl_FragColor.rgb,
        flipColor,
        uBlendStrength);
```

The result was dramatically brighter while still preserving highlights
and reflections.

## Lightening Colours Automatically

Even after fixing the blending, some colours still felt darker than
expected.

Pure magenta (`#ff00ff`) looked much richer than the pastel effect I was
aiming for.

Rather than manually creating lighter colours, I convert every input
colour to HSL before it reaches the shader.

I increase the lightness component, clamp it, then convert back to RGB.

Now users can still choose simple hex values while the material
automatically produces brighter, softer automotive colours.

## The Bug That Took Longest to Find

Everything worked beautifully...

Until I changed colours.

The initial load looked perfect.

Switch to another colour...

Everything became darker.

At first I suspected colour spaces.

Then HSL conversion.

Then gamma correction.

None of those were the problem.

The real culprit was much simpler.

My uniforms only existed after `onBeforeCompile` had executed.

React's `useEffect` was occasionally firing before those uniforms had
been injected.

Sometimes they existed.

Sometimes they didn't.

The fix was simply checking they were available before updating them.

```jsx
if (!mat?.userData?.uColor1) return;
```

One small guard clause fixed a surprisingly frustrating bug.

## What I Learned

This project ended up teaching me far more about Three.js than I
expected.

A few takeaways:

- `MeshPhysicalMaterial` is incredibly extensible.
- `onBeforeCompile` is one of the most useful APIs in Three.js.
- Most visual issues aren't lighting problems---they're colour-space
  problems.
- Fresnel is a fantastic driver for stylised materials.
- Building on top of existing PBR materials is often better than
  starting from a completely custom shader.
- React state and shader uniforms don't always update in the order you
  expect.

## The Final Result

The finished material now supports:

- Three configurable colours
- Dynamic Fresnel blending
- Edge amplification
- Automatic colour lightening
- Physical reflections
- Clearcoat
- Iridescence
- Environment maps
- Runtime colour updates
- A reusable React Three Fiber component

What started as "I'll just make a flip paint material" became a
fascinating exploration of shader injection, colour theory, and the
rendering pipeline inside Three.js.

And honestly, those are my favourite kinds of projects---the ones that
teach you far more than you expected when you first sat down to write
them.

https://x-mods.co.uk/custom-controller-builder/
