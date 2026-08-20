import { createFileRoute } from "@tanstack/react-router";
import { legalBySlug } from "@/lib/legal";
import { LegalView } from "@/components/shared/LegalView";

const page = legalBySlug("privacy")!;

export const Route = createFileRoute("/privacy")({
  head: () => ({ meta: [{ title: `${page.title} | تزنویسه` }, { name: "description", content: page.lead }] }),
  component: () => <LegalView page={page} />,
});
