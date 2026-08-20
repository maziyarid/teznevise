import { createFileRoute } from "@tanstack/react-router";
import { GAMS_PAGE } from "@/lib/imported-pages";
import { importedToPage } from "@/lib/page-copy";
import { ServicePage } from "@/components/shared/ServicePage";

const page = importedToPage(GAMS_PAGE);

export const Route = createFileRoute("/gams")({
  head: () => ({ meta: [{ title: `${page.title} | تزنویسه` }] }),
  component: () => <ServicePage page={page} fieldSlug="gams" />,
});
