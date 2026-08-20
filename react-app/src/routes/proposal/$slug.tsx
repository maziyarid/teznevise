import { createFileRoute, notFound } from "@tanstack/react-router";
import { PROPOSAL_PAGES } from "@/lib/content";
import { ServicePage } from "@/components/shared/ServicePage";

export const Route = createFileRoute("/proposal/$slug")({
  loader: ({ params }) => {
    const page = PROPOSAL_PAGES[params.slug];
    if (!page) throw notFound();
    return page;
  },
  component: Page,
});

function Page() {
  const page = Route.useLoaderData();
  return <ServicePage page={page} fieldSlug={`proposal-${page.slug}`} />;
}
