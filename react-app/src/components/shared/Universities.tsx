const UNIVERSITIES = [
  { name: "دانشگاه تهران", file: "tehran.svg" },
  { name: "دانشگاه صنعتی شریف", file: "sharif.webp" },
  { name: "دانشگاه صنعتی امیرکبیر", file: "amirkabir.svg" },
  { name: "دانشگاه علم و صنعت ایران", file: "iust.webp" },
  { name: "دانشگاه شهید بهشتی", file: "sbu.svg" },
  { name: "دانشگاه تربیت مدرس", file: "tmu.webp" },
  { name: "دانشگاه اصفهان", file: "isfahan.webp" },
  { name: "دانشگاه شیراز", file: "shiraz.webp" },
];

export function Universities({ compact }: { compact?: boolean }) {
  return (
    <section className={`uni-strip ${compact ? "is-compact" : ""}`} aria-label="همکاری با دانشگاه‌ها">
      <div className="container-tz">
        <p className="uni-kicker">همراه دانشجویان دانشگاه‌های برتر ایران</p>
        <ul className="uni-row">
          {UNIVERSITIES.map((u) => (
            <li key={u.name} className="uni-logo">
              <img
                src={`/assets/img/universities/${u.file}`}
                alt={u.name}
                width={compact ? 40 : 72}
                height={compact ? 40 : 72}
                loading="lazy"
                decoding="async"
              />
              <span>{u.name}</span>
            </li>
          ))}
        </ul>
      </div>
    </section>
  );
}
