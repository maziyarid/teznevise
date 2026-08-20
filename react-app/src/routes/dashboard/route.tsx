import { createFileRoute } from "@tanstack/react-router";
import { AppFrame } from "@/components/layout/AppFrame";

export const Route = createFileRoute("/dashboard")({
  component: () => <AppFrame kind="user" />,
});
