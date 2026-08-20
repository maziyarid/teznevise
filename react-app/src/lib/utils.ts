import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

/** Strip emoji / pictographs so chrome uses Font Awesome instead. */
export function stripEmoji(value: string) {
  return value
    .replace(/[\u{1F300}-\u{1FAFF}\u{2600}-\u{27BF}\u{2300}-\u{23FF}\u{FE0F}\u{200D}\u{2190}-\u{21FF}\u{2B00}-\u{2BFF}]/gu, "")
    .replace(/[✦★☆●○■□▪▫✔✅❌📌📝📊📈📚🎯💡🔬🧪🧠🌱🗂📖🌍📜⚗💊👥🔍📋📑🧫📐⚠⚖]/g, "")
    .replace(/^[\s✦•*-]+/, "")
    .replace(/\s{2,}/g, " ")
    .trim();
}
