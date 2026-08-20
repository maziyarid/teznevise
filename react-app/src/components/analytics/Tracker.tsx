import { useEffect } from "react";
import { useRouterState } from "@tanstack/react-router";
import { trackClick, trackPageView } from "@/lib/server/app";

export function Tracker() {
  const pathname = useRouterState({ select: (s) => s.location.pathname });

  useEffect(() => {
    if (pathname.startsWith("/admin") || pathname.startsWith("/dashboard") || pathname.startsWith("/login")) {
      return;
    }
    void trackPageView({ data: { path: pathname, referrer: document.referrer || undefined } }).catch(() => {});
  }, [pathname]);

  useEffect(() => {
    const onClick = (event: MouseEvent) => {
      const target = event.target as HTMLElement | null;
      if (!target) return;
      const el = target.closest("a,button") as HTMLElement | null;
      if (!el) return;
      const label =
        (el.getAttribute("aria-label") || el.textContent || el.getAttribute("href") || "click")
          .replace(/\s+/g, " ")
          .trim()
          .slice(0, 120);
      const href = el instanceof HTMLAnchorElement ? el.getAttribute("href") ?? undefined : undefined;
      void trackClick({
        data: {
          path: window.location.pathname,
          label: label || "click",
          href,
          x: Math.round(event.clientX),
          y: Math.round(event.clientY),
        },
      }).catch(() => {});
    };
    document.addEventListener("click", onClick, { capture: true });
    return () => document.removeEventListener("click", onClick, { capture: true });
  }, []);

  return null;
}
