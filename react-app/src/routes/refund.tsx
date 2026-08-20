import { createFileRoute } from "@tanstack/react-router";
import { legalBySlug } from "@/lib/legal";
import { LegalView } from "@/components/shared/LegalView";

const page = legalBySlug("refund")!;

export const Route = createFileRoute("/refund")({
  head: () => ({ meta: [{ title: `${page.title} | تزنویسه` }, { name: "description", content: page.lead }] }),
  component: () => <LegalView page={page} fieldSlug="legal-refund-policy" />,
});
