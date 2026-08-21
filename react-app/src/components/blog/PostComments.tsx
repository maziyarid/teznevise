import { useCallback, useEffect, useState, type FormEvent } from "react";
import { Link } from "@tanstack/react-router";
import { listApprovedComments, submitComment } from "@/lib/comments";
import { useCurrentUserState } from "@/lib/auth/use-current-user";
import { toast } from "sonner";

export function PostComments({ slug }: { slug: string }) {
  const { user } = useCurrentUserState();
  const [rows, setRows] = useState<Awaited<ReturnType<typeof listApprovedComments>>>([]);
  const [body, setBody] = useState("");
  const [busy, setBusy] = useState(false);

  const load = useCallback(() => {
    void listApprovedComments({ data: { slug } }).then(setRows).catch(() => setRows([]));
  }, [slug]);
  useEffect(() => {
    load();
  }, [load]);

  async function onSubmit(e: FormEvent) {
    e.preventDefault();
    setBusy(true);
    try {
      const res = await submitComment({ data: { slug, body } });
      setBody("");
      if (res.status === "approved") {
        toast.success(res.coins ? `نظر منتشر شد و ${res.coins} تزکوین گرفتید.` : "نظر شما منتشر شد.");
        load();
      } else {
        toast.message("نظر برای بررسی نگه داشته شد.", { description: res.reason });
      }
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "برای نظر دادن وارد شوید");
    } finally {
      setBusy(false);
    }
  }

  return (
    <section className="comments-block">
      <h2 className="text-2xl font-extrabold">گفتگو درباره این مطلب</h2>
      <p className="text-sm text-muted">فقط اعضای واردشده می‌توانند نظر بدهند. نظرهای علمی پس از بررسی هوشمند منتشر و پاداش تزکوین می‌گیرند.</p>
      <ol className="mt-6 space-y-3">
        {rows.map((c) => (
          <li key={c.id} className="comment-card">
            <b>{c.name}</b>
            <p className="mt-1 mb-0 whitespace-pre-wrap">{c.body}</p>
          </li>
        ))}
        {rows.length === 0 ? <p className="text-sm text-muted">هنوز نظری منتشر نشده. اولین نفر باشید.</p> : null}
      </ol>
      {!user ? (
        <p className="surface-card mt-6 text-sm">
          برای مشارکت در گفتگو{" "}
          <Link to="/login" className="font-extrabold text-brand">
            وارد شوید
          </Link>
          . پس از تکمیل پروفایل ۱۰۰۰ تزکوین هدیه می‌گیرید.
        </p>
      ) : (
        <form className="surface-card mt-6 grid gap-3" onSubmit={onSubmit}>
          <div className="field">
            <label htmlFor="cb">نظر پژوهشی</label>
            <textarea id="cb" rows={4} value={body} onChange={(e) => setBody(e.target.value)} required minLength={8} />
          </div>
          <button className="btn-tz btn-primary-tz" type="submit" disabled={busy}>
            {busy ? "در حال بررسی هوشمند…" : "ارسال نظر"}
          </button>
        </form>
      )}
    </section>
  );
}
