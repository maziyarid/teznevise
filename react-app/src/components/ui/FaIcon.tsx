import { cn } from "@/lib/utils";

export function FaIcon({
  icon,
  className,
}: {
  icon: string;
  className?: string;
}) {
  const name = icon.startsWith("fa-") ? icon : `fa-${icon}`;
  return <i className={cn("fa-solid", name, className)} aria-hidden />;
}
