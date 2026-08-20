import { Link } from "@tanstack/react-router";
import { cn } from "@/lib/utils";
import { SITE } from "@/lib/site";

export function Logo({
  className,
  to = "/",
}: {
  className?: string;
  to?: "/" | "/dashboard" | "/admin";
}) {
  return (
    <Link to={to} className={cn("brand", className)} aria-label={SITE.name}>
      <img
        src="/logo.png"
        alt={`لوگوی ${SITE.name}، مشاوره پایان‌نامه و پروپوزال`}
        width={128}
        height={48}
        decoding="async"
        fetchPriority="high"
      />
    </Link>
  );
}
