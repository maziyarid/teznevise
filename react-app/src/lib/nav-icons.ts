const RULES: [string, string][] = [
  ["chapter-one", "fa-file-lines"],
  ["chapter-two", "fa-book-open"],
  ["chapter-three", "fa-flask"],
  ["chapter-four", "fa-chart-column"],
  ["chapter-five", "fa-flag"],
  ["humanities", "fa-landmark"],
  ["engineering", "fa-gears"],
  ["pure-science", "fa-atom"],
  ["medical-health", "fa-heart-pulse"],
  ["art-architecture", "fa-palette"],
  ["agriculture", "fa-leaf"],
  ["animal", "fa-paw"],
  ["interdisciplinary", "fa-puzzle-piece"],
  ["international", "fa-globe"],
  ["/thesis/phd", "fa-user-graduate"],
  ["/proposal/phd", "fa-user-graduate"],
  ["/proposal/project", "fa-folder-open"],
  ["/proposal/english", "fa-language"],
  ["/proposal/qualitative", "fa-comments"],
  ["/proposal/quantitative", "fa-chart-pie"],
  ["/proposal/applied", "fa-wrench"],
  ["/proposal/medical", "fa-stethoscope"],
  ["psychology", "fa-brain"],
  ["law", "fa-scale-balanced"],
  ["management", "fa-briefcase"],
  ["philosophy", "fa-scroll"],
  ["history", "fa-landmark"],
  ["social-sciences", "fa-users"],
  ["descriptive-statistics", "fa-chart-simple"],
  ["sample-size", "fa-users"],
  ["cronbach", "fa-percent"],
  ["pearson", "fa-link"],
  ["spearman", "fa-arrow-trend-up"],
  ["t-test", "fa-not-equal"],
  ["regression", "fa-chart-line"],
  ["anova", "fa-table"],
  ["chi-square", "fa-border-all"],
  ["method-advisor", "fa-wand-magic-sparkles"],
  ["content-validity", "fa-clipboard-check"],
  ["/tools", "fa-calculator"],
  ["/proposal", "fa-file-lines"],
  ["/thesis", "fa-graduation-cap"],
  ["/blog", "fa-newspaper"],
  ["/contact", "fa-phone"],
  ["/about", "fa-circle-info"],
  ["/inquiry", "fa-pen-to-square"],
  ["/simulation", "fa-microchip"],
  ["/project", "fa-folder-open"],
  ["/article", "fa-newspaper"],
  ["/statistics", "fa-chart-line"],
];

export function navIconFor(to: string, label = ""): string {
  if (to === "/") return "fa-house";
  if (to === "#" || to === "") {
    if (label.includes("فصل")) return "fa-list-ol";
    if (label.includes("رشته")) return "fa-tags";
    if (label.includes("سایر")) return "fa-ellipsis";
    return "fa-circle-dot";
  }
  const path = to.toLowerCase();
  for (const [needle, icon] of RULES) {
    if (path.includes(needle)) return icon;
  }
  if (label.includes("پایان")) return "fa-graduation-cap";
  if (label.includes("پروپوزال")) return "fa-file-lines";
  if (label.includes("ابزار")) return "fa-calculator";
  if (label.includes("بلاگ")) return "fa-newspaper";
  if (label.includes("تماس")) return "fa-phone";
  return "fa-circle-dot";
}
