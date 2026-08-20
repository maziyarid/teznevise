import { createFileRoute } from "@tanstack/react-router";
import { SERVICE_PAGES } from "@/lib/content";
import { ServicePage } from "@/components/shared/ServicePage";

export const Route = createFileRoute("/thesis/")({ component: Thesis });

function Thesis() {
  return <ServicePage page={SERVICE_PAGES.thesis} />;
}
