import { createFileRoute, notFound } from "@tanstack/react-router";
import { resolveTool } from "@/lib/content";
import { ToolsWorkspace } from "@/components/tools/ToolsWorkspace";

export const Route = createFileRoute("/online-calculation-tools/$slug")({
  loader: ({ params }) => {
    const tool = resolveTool(params.slug);
    if (!tool) throw notFound();
    return tool;
  },
  head: ({ loaderData }) => ({
    meta: [{ title: loaderData ? `${loaderData.title} | ابزار تزنویسه` : "ابزار" }],
  }),
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
