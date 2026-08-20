import { Link2, Send } from "lucide-react";
import { claimShareReward } from "@/lib/server/wallet";
import { useCurrentUserState } from "@/lib/auth/use-current-user";
import { toast } from "sonner";

export function ShareBar({ slug, title }: { slug: string; title: string }) {
  const { user } = useCurrentUserState();
  const url = typeof window !== "undefined" ? `${window.location.origin}/blog/${slug}` : `/blog/${slug}`;

  async function share(network: "telegram" | "whatsapp" | "x" | "linkedin", href: string) {
    window.open(href, "_blank", "noopener,noreferrer");
    if (!user) {
      toast.message("وارد شوید تا با اشتراک‌گذاری تزکوین بگیرید.");
      return;
    }
    try {
      const res = await claimShareReward({ data: { slug, network } });
      if (res.awarded) toast.success(`${res.coins} تزکوین برای اشتراک واریز شد`);
    } catch {
      /* ignore */
    }
  }

  return (
    <div className="share-bar">
      <span>اشتراک‌گذاری و کسب تزکوین</span>
      <button
        type="button"
        className="icon-btn"
        aria-label="تلگرام"
        onClick={() => share("telegram", `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`)}
      >
        <Send className="size-4" />
      </button>
      <button
        type="button"
        className="icon-btn"
        aria-label="واتساپ"
        onClick={() => share("whatsapp", `https://wa.me/?text=${encodeURIComponent(title + " " + url)}`)}
      >
        WA
      </button>
      <button
        type="button"
        className="icon-btn"
        aria-label="کپی لینک"
        onClick={() => {
          void navigator.clipboard.writeText(url);
          toast.success("لینک کپی شد");
          if (user) void claimShareReward({ data: { slug, network: "x" } }).then((r) => r.awarded && toast.success(`${r.coins} تزکوین`));
        }}
      >
        <Link2 className="size-4" />
      </button>
    </div>
  );
}
