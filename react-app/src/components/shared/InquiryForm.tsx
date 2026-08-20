import { useState } from "react";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import { submitInquiry, type InquiryInput } from "@/lib/inquiries";

const SERVICES = [
  "انجام پایان‌نامه",
  "انجام پروپوزال",
  "تحلیل آماری",
  "شبیه‌سازی",
  "تحلیل کیفی",
  "پروژه دانشجویی",
  "انجام مقاله",
  "سایر",
];

export function InquiryForm({ compact = false }: { compact?: boolean }) {
  const [done, setDone] = useState(false);
  const { register, handleSubmit, formState, reset } = useForm<InquiryInput>({
    defaultValues: {
      name: "",
      phone: "",
      degree: "کارشناسی ارشد",
      field: "",
      service: SERVICES[0],
      message: "",
    },
  });

  async function onSubmit(values: InquiryInput) {
    try {
      await submitInquiry({ data: values });
      setDone(true);
      reset();
      toast.success("درخواست شما ثبت شد. به‌زودی تماس می‌گیریم.");
    } catch {
      toast.error("ثبت درخواست انجام نشد. دوباره تلاش کنید.");
    }
  }

  if (done) {
    return (
      <div className="lead-card text-center">
        <h3 className="mb-2 text-xl font-extrabold text-brand">درخواست ثبت شد</h3>
        <p className="text-muted">
          کارشناسان تزنویسه در ساعات کاری با شما تماس می‌گیرند. اطلاعات شما محرمانه می‌ماند.
        </p>
        <button type="button" className="btn-tz btn-primary-tz mt-5" onClick={() => setDone(false)}>
          ارسال درخواست دیگر
        </button>
      </div>
    );
  }

  return (
    <form className="lead-card" onSubmit={handleSubmit(onSubmit)} noValidate>
      <div className="lead-card-head mb-4">
        <h3 className="m-0 text-xl font-extrabold">ثبت درخواست مشاوره</h3>
        <p className="mt-1 mb-0 text-sm text-muted">فرم رایگان است و پاسخ در ساعات کاری ارسال می‌شود.</p>
      </div>
      <div className={`grid gap-3 ${compact ? "grid-cols-1" : "sm:grid-cols-2"}`}>
        <div className="field">
          <label htmlFor="name">نام و نام خانوادگی</label>
          <input id="name" placeholder="نام شما" {...register("name", { required: true, minLength: 2 })} />
        </div>
        <div className="field">
          <label htmlFor="phone">شماره تماس</label>
          <input id="phone" placeholder="09xxxxxxxxx" dir="ltr" {...register("phone", { required: true, minLength: 8 })} />
        </div>
        <div className="field">
          <label htmlFor="degree">مقطع</label>
          <select id="degree" {...register("degree")}>
            <option>کارشناسی ارشد</option>
            <option>دکتری</option>
            <option>کارشناسی</option>
            <option>سایر</option>
          </select>
        </div>
        <div className="field">
          <label htmlFor="service">نوع خدمت</label>
          <select id="service" {...register("service")}>
            {SERVICES.map((s) => (
              <option key={s}>{s}</option>
            ))}
          </select>
        </div>
        <div className="field sm:col-span-2">
          <label htmlFor="field">رشته / گرایش</label>
          <input id="field" placeholder="مثلاً مدیریت بازرگانی" {...register("field")} />
        </div>
        <div className="field sm:col-span-2">
          <label htmlFor="message">توضیح کوتاه</label>
          <textarea id="message" placeholder="موضوع، مرحله فعلی و ددلاین..." {...register("message")} />
        </div>
      </div>
      <button className="btn-tz btn-primary-tz mt-4 w-full" type="submit" disabled={formState.isSubmitting}>
        {formState.isSubmitting ? "در حال ارسال..." : "ارسال درخواست مشاوره"}
      </button>
      <p className="mt-2 mb-0 text-center text-[11px] text-muted">
        اطلاعات شما برای بررسی درخواست استفاده می‌شود و محرمانه خواهد ماند.
      </p>
    </form>
  );
}
