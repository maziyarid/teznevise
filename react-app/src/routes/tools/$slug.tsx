import { createFileRoute, notFound } from "@tanstack/react-router";
import { TOOLS } from "@/lib/content";
import { ToolsWorkspace } from "@/components/tools/ToolsWorkspace";

export const Route = createFileRoute("/tools/$slug")({
  loader: ({ params }) => {
    const tool = TOOLS.find((t) => t.slug === params.slug);
    if (!tool) throw notFound();
    return tool;
  },
  head: ({ loaderData }) => ({
    meta: [{ title: loaderData ? `${loaderData.title} | ابزار تزنویسه` : "ابزار" }],
  }),
  component: ToolPage,
});

function ToolPage() {
  const tool = Route.useLoaderData();
  return (
    <section className="section pt-6">
      <div className="container-tz">
        <ToolsWorkspace tool={tool} />
      </div>
    </section>
  );
}
