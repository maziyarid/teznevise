import { createRootRoute, HeadContent, Outlet, Scripts } from "@tanstack/react-router";
import { AuthProvider } from "@/lib/auth/provider";
import { PreviewHostBridge } from "@/components/preview-host-bridge";
import { SiteShell } from "@/components/layout/SiteShell";
import { Toaster } from "sonner";
import appCss from "../styles.css?url";

const APP_NAME = "تزنویسه";

export const Route = createRootRoute({
  head: () => ({
    meta: [
      { charSet: "utf-8" },
      { name: "viewport", content: "width=device-width, initial-scale=1" },
      { title: APP_NAME },
      { name: "theme-color", content: "#145D4A" },
      {
        name: "description",
        content: "تزنویسه؛ مشاوره تخصصی پایان‌نامه، پروپوزال و تحلیل آماری با حفظ محرمانگی.",
      },
      { name: "robots", content: "index,follow" },
      { property: "og:locale", content: "fa_IR" },
      { property: "og:type", content: "website" },
      { property: "og:site_name", content: APP_NAME },
    ],
    links: [
      { rel: "icon", type: "image/svg+xml", href: "/favicon.svg" },
      {
        rel: "preload",
        href: "/fonts/Vazirmatn-Regular.woff2",
        as: "font",
        type: "font/woff2",
        crossOrigin: "anonymous",
      },
      {
        rel: "preload",
        href: "/fonts/Vazirmatn-Bold.woff2",
        as: "font",
        type: "font/woff2",
        crossOrigin: "anonymous",
      },
      { rel: "stylesheet", href: appCss },
      { rel: "manifest", href: "/__grok/manifest.webmanifest" },
      { rel: "apple-touch-icon", href: "/__grok/icon-180.png" },
    ],
  }),
  component: Root,
});

function Root() {
  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "EducationalOrganization",
    name: "تزنویسه",
    url: "https://teznevise.ir",
    logo: "https://teznevise.ir/logo.png",
    telephone: "+989302822091",
    address: {
      "@type": "PostalAddress",
      addressLocality: "تهران",
      streetAddress: "انقلاب، خیابان ۱۲ فروردین",
      addressCountry: "IR",
    },
  };
  return (
    <html lang="fa" dir="rtl" suppressHydrationWarning>
      <head>
        <HeadContent />
        <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }} />
      </head>
      <body>
        <PreviewHostBridge />
        <AuthProvider>
          <SiteShell>
            <Outlet />
          </SiteShell>
        </AuthProvider>
        <Toaster position="top-center" richColors dir="rtl" />
        <Scripts />
      </body>
    </html>
  );
}
