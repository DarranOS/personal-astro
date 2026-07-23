---
title: "Rolling out AI-augmented delivery at Jarilo"
subtitle: "How a 41% output increase actually happened, process by process."
tags: ["Wordpress"]
poster: "./images/image-1.webp"
---

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
