import { createFileRoute } from "@tanstack/react-router";
import { ARTICLES, SERVICES, TOOLS } from "@/lib/content";
import { FOOTER_NAV, FOOTER_QUICK, FOOTER_SERVICES, PRIMARY_NAV } from "@/lib/site";

function urls() {
  const list = [
    "/",
    "/privacy",
    "/terms",
    "/cookies",
    "/refund",
    "/rules",
    "/search",
    "/sitemap",
    ...PRIMARY_NAV.map((n) => n.to),
    ...FOOTER_SERVICES.map((n) => n.to),
    ...FOOTER_NAV.map((n) => n.to),
    ...FOOTER_QUICK.map((n) => n.to),
    ...SERVICES.map((s) => s.to),
    ...TOOLS.map((t) => `/tools/${t.slug}`),
    ...ARTICLES.map((a) => `/blog/${a.slug}`),
  ];
  return Array.from(new Set(list));
}

export const Route = createFileRoute("/sitemap.xml")({
  server: {
    handlers: {
      GET: () => {
        const body = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${urls()
  .map(
    (u) => `  <url><loc>https://teznevise.ir${u}</loc><changefreq>weekly</changefreq></url>`,
  )
  .join("\n")}
</urlset>`;
        return new Response(body, {
          headers: { "Content-Type": "application/xml; charset=utf-8" },
        });
      },
    },
  },
});
