/** Remove decorative emoji while preserving ordinary mathematical and directional copy. */
const EMOJI_OR_DECORATION = /\p{Extended_Pictographic}|\p{Emoji_Modifier}|\u{FE0F}|\u{200D}|[✦★☆●○■□▪▫✔❌]/gu;

/** Strip emoji / pictographs so chrome uses Font Awesome instead. */
export function stripEmoji(value: string) {
  return value
    .replace(EMOJI_OR_DECORATION, "")
    .replace(/^[\s✦•*-]+/u, "")
    .replace(/\s{2,}/g, " ")
    .trim();
}
