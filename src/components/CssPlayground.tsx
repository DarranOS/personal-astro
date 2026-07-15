import { useState, useMemo } from "preact/hooks";

const DEFAULT_HTML = ` <select>
    <button>
    <selectedcontent></selectedcontent>
    </button>
    
    <option>Yes</option>
    <option>No</option>
  
    </select>`;

const DEFAULT_CSS = `.card {
  padding: 1.5rem;
  border-radius: 8px;
  background: #fdfaf6;
  border: 1px solid #e5ddd3;
  font-family: sans-serif;
}
.card h3 {
  margin: 0 0 0.5rem;
}
.card button {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  background: #b5654a;
  color: white;
  cursor: pointer;
}`;

export default function CssPlayground() {
  const [html, setHtml] = useState(DEFAULT_HTML);
  const [css, setCss] = useState(DEFAULT_CSS);

  const srcDoc = useMemo(
    () => `<!doctype html><html><head><style>
      body { margin: 0; padding: 1.5rem; }
      ${css}
    </style></head><body>${html}</body></html>`,
    [html, css],
  );

  return (
    <div class="playground">
      <div class="playground-editors">
        <label>
          HTML
          <textarea
            value={html}
            onInput={(e) => setHtml((e.target as HTMLTextAreaElement).value)}
            spellcheck={false}
          />
        </label>
        <label>
          CSS
          <textarea
            value={css}
            onInput={(e) => setCss((e.target as HTMLTextAreaElement).value)}
            spellcheck={false}
          />
        </label>
      </div>
      <iframe
        class="playground-preview"
        srcDoc={srcDoc}
        sandbox="allow-same-origin"
      />
    </div>
  );
}
