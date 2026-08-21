import { createFileRoute } from "@tanstack/react-router";
import { SERVICE_PAGES } from "@/lib/content";
import { ServicePage } from "@/components/shared/ServicePage";

export const Route = createFileRoute("/simulation")({
  head: () => ({ meta: [{ title: `${SERVICE_PAGES.simulation.title} | تزنویسه` }] }),
  component: Sim,
});

function Sim() {
  return <ServicePage page={SERVICE_PAGES.simulation} fieldSlug="simulation" />;
}
