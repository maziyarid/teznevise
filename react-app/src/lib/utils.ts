import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export { stripEmoji } from "./emoji";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}
