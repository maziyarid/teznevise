import { createFileRoute } from "@tanstack/react-router";
import { HomePage } from "@/components/home/HomePage";
import { SITE } from "@/lib/site";

export const Route = createFileRoute("/")({
  head: () => ({
    meta: [
      { title: `${SITE.name} | مشاوره پایان‌نامه، پروپوزال و آمار` },
      { name: "description", content: SITE.description },
      { property: "og:title", content: SITE.name },
      { property: "og:description", content: SITE.description },
      { property: "og:image", content: "/og.jpg" },
      { name: "twitter:card", content: "summary_large_image" },
    ],
    links: [{ rel: "canonical", href: "https://teznevise.ir/" }],
  }),
  component: HomePage,
});
