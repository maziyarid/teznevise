import { createFileRoute } from "@tanstack/react-router";
import { SERVICE_PAGES } from "@/lib/content";
import { ServicePage } from "@/components/shared/ServicePage";

export const Route = createFileRoute("/project")({
  head: () => ({ meta: [{ title: `${SERVICE_PAGES.project.title} | تزنویسه` }] }),
  component: () => <ServicePage page={SERVICE_PAGES.project} fieldSlug="project" />,
});
