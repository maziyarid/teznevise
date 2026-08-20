import { createFileRoute, notFound, redirect } from "@tanstack/react-router";
import { resolveTool } from "@/lib/content";
import { resolveWpSlug } from "@/lib/wp-aliases";
import { ToolsWorkspace } from "@/components/tools/ToolsWorkspace";
import { ServicePage } from "@/components/shared/ServicePage";
import { StaticPageView } from "@/components/shared/StaticPageView";
import { LegalView } from "@/components/shared/LegalView";

export const Route = createFileRoute("/$slug")({
  loader: ({ params }) => {
    const hit = resolveWpSlug(params.slug);
    if (!hit) throw notFound();
    if (hit.kind === "redirect") throw redirect({ to: hit.to });
    if (hit.kind === "tool") {
      const tool = resolveTool(hit.slug);
      if (!tool) throw notFound();
      return { hit, tool };
    }
    return { hit };
  },
  head: ({ loaderData }) => {
    const hit = loaderData?.hit;
    const title =
      hit?.kind === "tool"
        ? loaderData?.tool?.title
        : hit?.kind === "service"
          ? hit.page.title
          : hit?.kind === "legal"
            ? hit.page.title
            : "تزنویسه";
    return { meta: [{ title: title ? `${title} | تزنویسه` : "تزنویسه" }] };
  },
  component: AliasPage,
});

function AliasPage() {
  const data = Route.useLoaderData();
  const hit = data.hit;
  if (hit.kind === "tool" && data.tool) {
    return (
      <section className="section pt-6">
        <div className="container-tz">
          <ToolsWorkspace tool={data.tool} />
        </div>
      </section>
    );
  }
  if (hit.kind === "service") {
    return <ServicePage page={hit.page} fieldSlug={hit.fieldSlug} />;
  }
  if (hit.kind === "static") {
    return <StaticPageView slug={hit.slug} />;
  }
  if (hit.kind === "legal") {
    return (
      <LegalView
        page={{
          slug: hit.page.slug,
          eyebrow: "قوانین",
          title: hit.page.title,
          lead: hit.page.lead,
          sections: hit.page.sections,
        }}
        fieldSlug={`legal-${hit.page.slug}`}
      />
    );
  }
  return null;
}
