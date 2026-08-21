import { useCallback, useEffect, useState } from "react";
import { Paperclip, Download, Shield } from "lucide-react";
import { downloadVaultFile, listVaultFiles, uploadVaultFile } from "@/lib/server/vault";
import { toast } from "sonner";

export function TicketFiles({ ticketId }: { ticketId: string }) {
  const [rows, setRows] = useState<Awaited<ReturnType<typeof listVaultFiles>>>([]);
  const [busy, setBusy] = useState(false);

  const load = useCallback(() => {
    void listVaultFiles({ data: { ticketId } }).then(setRows).catch(() => setRows([]));
  }, [ticketId]);
  useEffect(() => {
    load();
  }, [load]);

  async function onFile(file: File) {
    setBusy(true);
    try {
      const base64 = await fileToB64(file);
      await uploadVaultFile({ data: { ticketId, filename: file.name, mime: file.type || "application/octet-stream", base64 } });
      toast.success("فایل در انبار امن ذخیره شد");
      load();
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "آپلود ممکن نشد");
    } finally {
      setBusy(false);
    }
  }

  return (
    <section className="surface-card mt-4">
      <div className="mb-3 flex items-center gap-2">
        <Shield className="size-4 text-brand" />
        <h3 className="m-0 text-base font-extrabold">پیوست امن</h3>
      </div>
      <p className="mt-0 text-sm text-muted">
        فایل‌ها در پایگاه جدا از محتوای عمومی ذخیره می‌شوند و فقط شما و پشتیبان می‌توانید دانلود کنید.
      </p>
      <label className="btn-tz btn-light-tz mt-2 inline-flex cursor-pointer">
        <Paperclip className="size-4" />
        {busy ? "در حال بارگذاری…" : "انتخاب فایل (تا ۲ مگابایت)"}
        <input
          type="file"
          className="sr-only"
          disabled={busy}
          accept=".pdf,.png,.jpg,.jpeg,.webp,.zip,.doc,.docx,.xlsx,.txt,.md"
          onChange={(e) => {
            const f = e.target.files?.[0];
            if (f) void onFile(f);
            e.target.value = "";
          }}
        />
      </label>
      <ul className="mt-3 space-y-2">
        {rows.map((f) => (
          <li key={f.id} className="flex items-center justify-between gap-3 text-sm">
            <span className="truncate font-bold">{f.filename}</span>
            <button
              type="button"
              className="icon-btn"
              aria-label={`دانلود ${f.filename}`}
              onClick={() => void saveLocal(f.id)}
            >
              <Download className="size-4" />
            </button>
          </li>
        ))}
      </ul>
    </section>
  );
}

function fileToB64(file: File) {
  return new Promise<string>((resolve, reject) => {
    const r = new FileReader();
    r.onload = () => resolve(String(r.result));
    r.onerror = () => reject(new Error("خواندن فایل ممکن نشد"));
    r.readAsDataURL(file);
  });
}

async function saveLocal(id: string) {
  try {
    const file = await downloadVaultFile({ data: { id } });
    const bin = Uint8Array.from(atob(file.base64), (c) => c.charCodeAt(0));
    const blob = new Blob([bin], { type: file.mime });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = file.filename;
    a.click();
    URL.revokeObjectURL(url);
  } catch {
    toast.error("دانلود مجاز نیست");
  }
}
