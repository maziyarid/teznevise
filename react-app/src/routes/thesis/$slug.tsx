import { createFileRoute, notFound } from "@tanstack/react-router";
import { THESIS_PAGES } from "@/lib/content";
import { ServicePage } from "@/components/shared/ServicePage";

export const Route = createFileRoute("/thesis/$slug")({
  loader: ({ params }) => {
    const page = THESIS_PAGES[params.slug];
    if (!page) throw notFound();
    return page;
  },
  head: ({ loaderData }) => ({
    meta: [{ title: loaderData ? `${loaderData.title} | تزنویسه` : "پایان‌نامه" }],
  }),
  component: Page,
});

function Page() {
  const page = Route.useLoaderData();
  return <ServicePage page={page} fieldSlug={`thesis-${page.slug}`} />;
}