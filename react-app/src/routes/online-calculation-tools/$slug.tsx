import { createFileRoute, notFound } from "@tanstack/react-router";
import { resolveTool, toolPageCopy } from "@/lib/content";
import { ToolsWorkspace } from "@/components/tools/ToolsWorkspace";

export const Route = createFileRoute("/online-calculation-tools/$slug")({
  loader: ({ params }) => {
    const tool = resolveTool(params.slug);
    if (!tool) throw notFound();
    return tool;
  },
  head: ({ loaderData }) => {
    const copy = loaderData ? toolPageCopy(loaderData.slug) : null;
    const title = copy?.heroTitle || loaderData?.title;
    return { meta: [{ title: title ? `${title} | تزنویسه` : "ابزار" }] };
  },
  component: Page,
});

function Page() {
  const tool = Route.useLoaderData();
  return (
    <section className="section pt-6">
      <div className="container-tz">
        <ToolsWorkspace tool={tool} />
      </div>
    </section>
  );
}
