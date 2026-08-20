import { createFileRoute, Link, useRouterState } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { verifyPayment } from "@/lib/server/payments";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/pay/callback")({ component: PayCallback });

function PayCallback() {
  const search = useRouterState({ select: (s) => s.location.searchStr });
  const [msg, setMsg] = useState("در حال تأیید پرداخت…");
  const params = new URLSearchParams(search.startsWith("?") ? search.slice(1) : search);

  useEffect(() => {
    const authority = params.get("Authority") || params.get("authority") || undefined;
    const transid = params.get("transid") || params.get("transId") || undefined;
    const status = params.get("Status") || params.get("status") || undefined;
    void verifyPayment({ data: { authority, transid, status: status ?? undefined } })
      .then((r) => setMsg(`${r.coins} تزکوین به کیف پول افزوده شد.`))
      .catch((err) => setMsg(err instanceof Error ? err.message : "تأیید پرداخت ناموفق بود"));
  }, [search]);

  return (
    <>
      <PageHero eyebrow="پرداخت" title="بازگشت از درگاه" lead={msg} />
      <section className="section">
        <div className="container-tz text-center">
          <Link to="/dashboard/wallet" className="btn-tz btn-primary-tz">
            مشاهده کیف پول
          </Link>
        </div>
      </section>
    </>
  );
}
