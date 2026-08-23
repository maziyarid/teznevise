import { useState } from "react";
import { MessageCircle, Phone, Send, X } from "lucide-react";
import { SITE } from "@/lib/site";

export function Fab() {
  const [open, setOpen] = useState(false);
  return (
    <div className={`tz-fab-wrap tz-fab-wrap--start ${open ? "is-open" : ""}`}>
      {open ? (
        <div className="tz-fab-menu" role="menu">
          <a className="tz-fab-item" href={SITE.telegram} target="_blank" rel="noopener noreferrer" role="menuitem">
            <span className="tz-fab-ico tg" aria-hidden>
              <Send className="size-4" />
            </span>
            تلگرام
          </a>
          <a className="tz-fab-item" href={SITE.whatsapp} target="_blank" rel="noopener noreferrer" role="menuitem">
            <span className="tz-fab-ico wa" aria-hidden>
              <Phone className="size-4" />
            </span>
            واتساپ
          </a>
          <a className="tz-fab-item" href={SITE.bale} target="_blank" rel="noopener noreferrer" role="menuitem">
            <span className="tz-fab-ico bale" aria-hidden>
              ب
            </span>
            بله
          </a>
          <a className="tz-fab-item" href={`tel:${SITE.phoneIntl}`} role="menuitem">
            <span className="tz-fab-ico call" aria-hidden>
              <Phone className="size-4" />
            </span>
            تماس مستقیم
          </a>
        </div>
      ) : null}
      <button
        type="button"
        className="tz-fab-toggle"
        aria-expanded={open}
        aria-label={open ? "بستن راه‌های ارتباطی" : "باز کردن راه‌های ارتباطی"}
        onClick={() => setOpen((v) => !v)}
      >
        {open ? <X className="size-6" /> : <MessageCircle className="size-6" />}
      </button>
    </div>
  );
}
