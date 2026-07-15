import { useState, useMemo } from "preact/hooks";
import "../styles/select-example.css";

type Section = {
  id: string;
  selector: string;
  note?: string;
  defaultValue: string;
  defaultOpen?: boolean;
};

const SECTIONS: Section[] = [
  {
    id: "base",
    selector: "select",
    defaultValue: `appearance: base-select;
background-color: limegreen;
width: fit-content;`,
    defaultOpen: true,
  },
  {
    id: "picker",
    selector: "select::picker(select)",
    note: "the dropdown panel itself — also needs appearance: base-select",
    defaultValue: `appearance: base-select;
border-radius: 0.25rem;
background-color: var(--surface, #fff);
color: var(--grey-900, #1a1a1a);`,
    defaultOpen: true,
  },
  {
    id: "pickerOpen",
    selector: "select:open::picker(select)",
    note: "the picker panel while it's open — good for entry animations",
    defaultValue: `transition: transform 150ms ease, opacity 150ms ease;`,
  },
  {
    id: "pickerIconOpen",
    selector: "select:open::picker-icon",
    note: "arrow state while the picker is open",
    defaultValue: `rotate: 180deg;`,
  },
  {
    id: "pickerIcon",
    selector: "select::picker-icon",
    note: "the arrow indicator",
    defaultValue: `font-size: 1rem;
color: yellow;
align-content: center;
display: flex;
transition: 150ms ease;
rotate: 0deg;`,
  },
  {
    id: "optionChecked",
    selector: "select option:checked",
    defaultValue: `background-color: cyan;`,
  },
  {
    id: "checkmark",
    selector: "select option::checkmark",
    note: "replaces the tick next to the selected option",
    defaultValue: `content: "✓";
color: red;`,
  },
  {
    id: "selectedContent",
    selector: "select selectedcontent",
    note: "mirrors the chosen option's content into the closed button",
    defaultValue: `color: blue;`,
  },
];

const DEFAULT_HTML = `<select>
  <option>Cyan</option>
  <option selected>Magenta</option>
  <option>Yellow</option>
  <option>Key (black)</option>
</select>`;

export default function SelectPlayground() {
  const [html, setHtml] = useState(DEFAULT_HTML);
  const [values, setValues] = useState<Record<string, string>>(
    Object.fromEntries(SECTIONS.map((s) => [s.id, s.defaultValue])),
  );

  const css = useMemo(
    () =>
      SECTIONS.map((s) => `${s.selector} {\n${values[s.id]}\n}`).join("\n\n"),
    [values],
  );

  const srcDoc = useMemo(
    () => `<!doctype html><html><head><style>
      body { margin: 0; padding: 1rem; font-family: sans-serif; }
      ${css}
    </style></head><body>${html}</body></html>`,
    [html, css],
  );

  return (
    <div class="select-playground">
      <p class="playground-caption">
        customizable &lt;select&gt; — Chrome 134+ only, falls back gracefully
        elsewhere
      </p>

      <div class="select-playground-grid">
        <div class="html-fields">
          <label class="field">
            <span class="field-label">html</span>
            <textarea
              value={html}
              onInput={(e) => setHtml((e.target as HTMLTextAreaElement).value)}
              spellcheck={false}
              rows={15}
            />
          </label>
        </div>

        <div class="css-fields">
          {SECTIONS.map((s) => (
            <details class="field-accordion" open={s.defaultOpen} key={s.id}>
              <summary>
                <code>{s.selector}</code>
                {s.note && <span class="field-note">{s.note}</span>}
              </summary>
              <textarea
                value={values[s.id]}
                onInput={(e) =>
                  setValues((prev) => ({
                    ...prev,
                    [s.id]: (e.target as HTMLTextAreaElement).value,
                  }))
                }
                spellcheck={false}
                rows={12}
              />
            </details>
          ))}
        </div>
        <iframe
          class="select-playground-preview"
          srcDoc={srcDoc}
          sandbox="allow-same-origin"
        />
      </div>
    </div>
  );
}
