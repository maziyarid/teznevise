import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

const EMOJI_OR_SYMBOL = /\p{Extended_Pictographic}|\u{FE0F}|\u{200D}|[\u{2190}-\u{21FF}\u{2300}-\u{23FF}\u{2600}-\u{27BF}\u{2B00}-\u{2BFF}]/gu;

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

/** Strip emoji / pictographs so chrome uses Font Awesome instead. */
export function stripEmoji(value: string) {
  return value
    .replace(EMOJI_OR_SYMBOL, "")
    .replace(/^[\s✦•*-]+/u, "")
    .replace(/\s{2,}/g, " ")
    .trim();
}
