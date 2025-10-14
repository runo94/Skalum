import { readdir, readFile, writeFile } from "node:fs/promises";
import { extname, basename, join } from "node:path";
import ttf2woff2 from "ttf2woff2";

const DIR = "./assets/fonts/dm-sans"; // підстав свій шлях
const files = await readdir(DIR);
let count = 0;

for (const f of files) {
  if (extname(f).toLowerCase() !== ".ttf") continue;
  const src = join(DIR, f);
  const out = join(DIR, basename(f, ".ttf") + ".woff2");
  const buf = await readFile(src);
  const woff2 = ttf2woff2(buf);
  await writeFile(out, woff2);
  console.log(`✓ ${f} → ${basename(out)}`);
  count++;
}
console.log(`Done: ${count} file(s)`);
