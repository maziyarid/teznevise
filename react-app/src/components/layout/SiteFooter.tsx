import { Link } from "@tanstack/react-router";
import { Mail, MapPin, Phone, Send, ShieldCheck } from "lucide-react";
import { FOOTER_NAV, FOOTER_QUICK, FOOTER_SERVICES, SITE } from "@/lib/site";

export function SiteFooter() {
  const year = new Date().getFullYear();
  return (
    <footer className="site-footer site-footer-new footer-new">
      <div className="container-tz">
        <div className="footer-grid">
          <div className="footer-brand">
            <Link to="/" className="footer-logo-wrap" aria-label={SITE.name}>
              <img
                src="/logo.png"
                alt={`لوگوی ${SITE.name}`}
                width={116}
                height={44}
                loading="lazy"
                decoding="async"
              />
            </Link>
            <p>
              تزنویسه همراه پژوهشی دانشجویان و پژوهشگران؛ از انتخاب موضوع و تدوین
              پروپوزال تا تحلیل آماری، نگارش و آمادگی دفاع.
            </p>
            <p>{SITE.tagline}</p>
            <div className="footer-social">
              <a href={SITE.telegram} target="_blank" rel="noopener noreferrer" aria-label="تلگرام">
                <Send className="size-4" />
              </a>
              <a href={SITE.whatsapp} target="_blank" rel="noopener noreferrer" aria-label="واتساپ">
                <Phone className="size-4" />
              </a>
            </div>
          </div>

          <div className="footer-col">
            <h4>خدمات</h4>
            {FOOTER_SERVICES.map((l) => (
              <Link key={l.to} to={l.to}>
                {l.label}
              </Link>
            ))}
          </div>

          <div className="footer-col">
            <h4>ناوبری</h4>
            {FOOTER_NAV.map((l) => (
              <Link key={l.to} to={l.to}>
                {l.label}
              </Link>
            ))}
          </div>

          <div className="footer-col">
            <h4>ارتباط و قوانین</h4>
            <a href={`tel:${SITE.phoneIntl}`}>
              <Phone className="ml-1 inline size-3.5" /> {SITE.phoneDisplay}
            </a>
            <a href={`mailto:${SITE.email}`}>
              <Mail className="ml-1 inline size-3.5" /> {SITE.email}
            </a>
            <p>
              <MapPin className="ml-1 inline size-3.5" /> {SITE.address}
            </p>
            <Link to="/privacy">حریم خصوصی</Link>
            <Link to="/terms">شرایط استفاده</Link>
            <Link to="/refund">بازگشت وجه</Link>
            <Link to="/cookies">سیاست کوکی</Link>
            <Link to="/rules">آیین‌نامه پژوهشی</Link>
          </div>
        </div>

        <div className="footer-certs" aria-label="نمادها و گواهی‌ها">
          <a className="trust-badge" href="/privacy" title="نماد اعتماد الکترونیکی">
            <svg viewBox="0 0 88 88" width="72" height="72" role="img" aria-labelledby="enamad-title">
              <title id="enamad-title">نماد اعتماد الکترونیکی</title>
              <rect x="4" y="4" width="80" height="80" rx="16" fill="#f7fcfa" stroke="#145D4A" />
              <text x="44" y="40" textAnchor="middle" fontSize="11" fontWeight="800" fill="#145D4A" fontFamily="Vazirmatn, Tahoma, sans-serif">
                اینماد
              </text>
              <text x="44" y="58" textAnchor="middle" fontSize="9" fill="#5a7268" fontFamily="Vazirmatn, Tahoma, sans-serif">
                Enamad
              </text>
            </svg>
            <span>اینماد</span>
          </a>
          <div className="trust-badge">
            <svg viewBox="0 0 88 88" width="72" height="72" role="img" aria-labelledby="samandehi-title">
              <title id="samandehi-title">ساماندهی</title>
              <rect x="4" y="4" width="80" height="80" rx="16" fill="#fbf7ee" stroke="#8a6a22" />
              <text x="44" y="40" textAnchor="middle" fontSize="11" fontWeight="800" fill="#8a6a22" fontFamily="Vazirmatn, Tahoma, sans-serif">
                ساماندهی
              </text>
              <text x="44" y="58" textAnchor="middle" fontSize="9" fill="#5a7268" fontFamily="Vazirmatn, Tahoma, sans-serif">
                Samandehi
              </text>
            </svg>
            <span>ساماندهی</span>
          </div>
          <div className="trust-badge">
            <ShieldCheck className="size-10 text-brand" aria-hidden />
            <span>SSL امن</span>
          </div>
        </div>

        <div className="footer-bottom">
          <span>
            © {year} {SITE.name} — تمامی حقوق محفوظ است.
          </span>
          <span>
            {FOOTER_QUICK.slice(0, 3).map((l) => (
              <Link key={l.to} to={l.to} className="mx-2">
                {l.label}
              </Link>
            ))}
          </span>
        </div>
      </div>
    </footer>
  );
}
