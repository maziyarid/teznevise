import type { ReactNode } from "react";
import { Link } from "@tanstack/react-router";

/** Escape hatch for content-driven paths (footer, search, menus). */
export function AppLink({
  to,
  children,
  className,
  params,
}: {
  to: string;
  children: ReactNode;
  className?: string;
  params?: Record<string, string>;
}) {
  return (
    <Link to={to as never} params={params as never} className={className}>
      {children}
    </Link>
  );
}
