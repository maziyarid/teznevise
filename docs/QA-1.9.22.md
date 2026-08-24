# QA 1.9.22

After deploy, visit wp-admin once so tables install and corpus indexing starts (20 posts per cron tick).

- [ ] `wp_teznevise_ai_corpus` and `wp_teznevise_ai_training` exist (`teznevise_ai_knowledge_v` = 1.0.0, `teznevise_ai_corpus_v` = 1.9.22)
- [ ] Live chat FAB shows «گفتگو» on desktop; panel hides FAB; thinking is a closed «استدلال» chip
- [ ] Sending a question does not dump the thinking stream; opening «استدلال» reveals it
- [ ] Hint chips hide after the first send and return on «گفتگوی تازه»
- [ ] Thumbs on a reply hit `POST /teznevise-ai/v1/chat/rate` and increment hub «مفید»
- [ ] Hub → «بازاندیس دانش سایت» re-queues corpus
- [ ] Collaborative / research still work when toggled; default is single agent
- [ ] Mobile panel sits above the bottom nav and does not cover the composer
- [ ] Enamad HTML in the footer is unchanged; no ساماندهی/ثبت ملی
- [ ] tools-ai.php is still unmounted
- [ ] FAQ accordion still collapses (hotfix-207 still last-wins before 208)
- [ ] Titles wrap; article images don’t overflow; account / 404 / search remain usable
