import { createFileRoute, redirect } from "@tanstack/react-router";

export const Route = createFileRoute("/online-calculation-tools/")({
  beforeLoad: () => {
    throw redirect({ to: "/tools" });
  },
  component: () => null,
});