import { createServerFn } from "@tanstack/react-start";
import { z } from "zod";
import { getSql } from "@/lib/db";
import { optionalAuthMiddleware } from "@/lib/server/optional-auth";

const inquirySchema = z.object({
  name: z.string().trim().min(2).max(120),
  phone: z.string().trim().min(8).max(20),
  email: z.string().trim().max(120).optional(),
  degree: z.string().trim().max(80).optional(),
  field: z.string().trim().max(120).optional(),
  service: z.string().trim().max(80).optional(),
  message: z.string().trim().max(2000).optional(),
});

export type InquiryInput = z.infer<typeof inquirySchema>;

export const submitInquiry = createServerFn({ method: "POST" })
  .middleware([optionalAuthMiddleware])
  .validator((data: unknown) => inquirySchema.parse(data))
  .handler(async ({ data, context }) => {
    const sql = await getSql();
    const id = crypto.randomUUID();
    await sql`
      insert into inquiries (id, user_id, name, phone, email, degree, field, service, message)
      values (
        ${id},
        ${context.userId},
        ${data.name},
        ${data.phone},
        ${data.email || null},
        ${data.degree || null},
        ${data.field || null},
        ${data.service || null},
        ${data.message || null}
      )
    `;
    return { ok: true as const, id };
  });
