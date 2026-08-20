import { createFileRoute } from "@tanstack/react-router";
import { legalBySlug } from "@/lib/legal";
import { LegalView } from "@/components/shared/LegalView";

const page = legalBySlug("rules")!;

export const Route = createFileRoute("/rules")({
  head: () => ({ meta: [{ title: `${page.title} | تزنویسه` }, { name: "description", content: page.lead }] }),
  component: () => <LegalView page={page} />,
});
