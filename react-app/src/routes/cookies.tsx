import { createFileRoute } from "@tanstack/react-router";
import { legalBySlug } from "@/lib/legal";
import { LegalView } from "@/components/shared/LegalView";

const page = legalBySlug("cookies")!;

export const Route = createFileRoute("/cookies")({
  head: () => ({ meta: [{ title: `${page.title} | تزنویسه` }, { name: "description", content: page.lead }] }),
  component: () => <LegalView page={page} fieldSlug="legal-cookie-policy" />,
});
