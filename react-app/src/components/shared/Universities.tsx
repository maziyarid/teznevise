const UNIVERSITIES = [
  { name: "دانشگاه تهران", mark: "ته" },
  { name: "شریف", mark: "شر" },
  { name: "امیرکبیر", mark: "ام" },
  { name: "علم و صنعت", mark: "صن" },
  { name: "شهید بهشتی", mark: "به" },
  { name: "تربیت مدرس", mark: "تم" },
  { name: "دانشگاه اصفهان", mark: "اص" },
  { name: "دانشگاه شیراز", mark: "شی" },
];

export function Universities({ compact }: { compact?: boolean }) {
  return (
    <section className={`uni-strip ${compact ? "is-compact" : ""}`} aria-label="همکاری با دانشگاه‌ها">
      <div className="container-tz">
        <p className="uni-kicker">همراه دانشجویان دانشگاه‌های برتر ایران</p>
        <ul className="uni-row">
          {UNIVERSITIES.map((u) => (
            <li key={u.name} className="uni-logo">
              <svg viewBox="0 0 72 72" width={compact ? 40 : 52} height={compact ? 40 : 52} role="img" aria-labelledby={`uni-${u.mark}`}>
                <title id={`uni-${u.mark}`}>{u.name}</title>
                <circle cx="36" cy="36" r="34" fill="#e8f5f1" stroke="#145D4A" strokeWidth="2" />
                <text x="36" y="42" textAnchor="middle" fontSize="18" fontWeight="800" fill="#145D4A" fontFamily="Vazirmatn, Tahoma, sans-serif">
                  {u.mark}
                </text>
              </svg>
              <span>{u.name}</span>
            </li>
          ))}
        </ul>
      </div>
    </section>
  );
}
