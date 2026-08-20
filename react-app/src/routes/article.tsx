import { createFileRoute } from "@tanstack/react-router";
import { SERVICE_PAGES } from "@/lib/content";
import { ServicePage } from "@/components/shared/ServicePage";

export const Route = createFileRoute("/article")({
  head: () => ({ meta: [{ title: `${SERVICE_PAGES.article.title} | تزنویسه` }] }),
  component: () => <ServicePage page={SERVICE_PAGES.article} fieldSlug="article" />,
});
