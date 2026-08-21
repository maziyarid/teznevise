import assert from "node:assert/strict";
import { readFileSync } from "node:fs";
import { dirname, join } from "node:path";
import test from "node:test";
import { fileURLToPath } from "node:url";
import ts from "typescript";

const root = join(dirname(fileURLToPath(import.meta.url)), "..");
const source = readFileSync(join(root, "src/lib/emoji.ts"), "utf8");
const compiled = ts.transpileModule(source, {
  compilerOptions: { module: ts.ModuleKind.ESNext, target: ts.ScriptTarget.ES2022 },
}).outputText;
const { stripEmoji } = await import(`data:text/javascript;base64,${Buffer.from(compiled).toString("base64")}`);

test("stripEmoji removes emoji sequences and skin-tone modifiers", () => {
  assert.equal(stripEmoji("Great 👍🏽 work"), "Great work");
});

test("stripEmoji preserves ordinary directional and mathematical copy", () => {
  assert.equal(stripEmoji("A → B ≠ C ● 🧫"), "A → B ≠ C");
});
