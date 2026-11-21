export type UserRole = 'admin' | 'secretary' | 'coordinator' | 'student';

export interface AuthUser {
  id: number;
  uuid: string;
  name: string;
  email: string;
  dni: string | null;
  phone: string | null;
  wa_digits: string | null;
  role: UserRole;
  is_active: boolean;
  must_change_password: boolean;
}

export interface ProgramItem {
  id: number;
  uuid: string;
  code: string;
  name: string;
  slug: string;
  type: 'course' | 'training' | 'advisory';
  description: string;
  syllabus_json: Array<{ module: string; hours?: number; topics: string[] }> | null;
  price: string;
  duration_hours: number;
  min_attendance_percentage: string;
  min_passing_grade: string;
  certification_label: string | null;
  image_path: string | null;
  is_published: boolean;
  status: string;
  cohorts?: CohortItem[];
}

export interface CohortSessionItem {
  id: number;
  uuid: string;
  cohort_id: number;
  session_number: number;
  title: string;
  session_date: string;
  start_time: string;
  end_time: string;
  session_type: 'virtual' | 'presential';
  meeting_url: string | null;
  classroom: string | null;
  location_name: string | null;
  location_map_url: string | null;
  recording_url: string | null;
  material_pdf_path: string | null;
  status: 'scheduled' | 'completed' | 'cancelled';
}

export interface CohortItem {
  id: number;
  uuid: string;
  code: string;
  program_id: number;
  coordinator_id: number | null;
  name: string;
  modality: 'virtual' | 'presential' | 'hybrid';
  whatsapp_group_link?: string | null;
  default_meeting_url?: string | null;
  location_name: string | null;
  location_address: string | null;
  location_map_url: string | null;
  start_date: string;
  end_date: string;
  max_capacity: number;
  enrolled_count: number;
  available_slots: number;
  status: string;
  program?: ProgramItem;
  coordinator?: AuthUser;
  sessions?: CohortSessionItem[];
}

export interface CertificateItem {
  id: number;
  uuid: string;
  enrollment_id: number;
  verification_uuid: string;
  certificate_code: string;
  qr_payload: string;
  snapshot_data: Record<string, unknown>;
  issued_at: string;
  revoked_at: string | null;
  revocation_reason: string | null;
  is_valid: boolean;
}

export interface PaymentItem {
  id: number;
  uuid: string;
  enrollment_id: number;
  amount: string;
  payment_method: 'yape' | 'plin' | 'transfer' | 'gateway' | 'cash';
  operation_code: string | null;
  voucher_path: string | null;
  status: 'pending' | 'validated' | 'rejected' | 'reversed';
  rejection_reason: string | null;
  validated_at: string | null;
  notes: string | null;
  enrollment?: EnrollmentItem;
  validator?: AuthUser;
  created_at: string;
}

export interface EnrollmentItem {
  id: number;
  uuid: string;
  user_id: number;
  cohort_id: number;
  enrollment_status: 'pending_payment' | 'enrolled' | 'completed' | 'cancelled';
  reservation_reference: string | null;
  reservation_token: string | null;
  reservation_expires_at: string | null;
  added_to_whatsapp: boolean;
  whatsapp_added_by: number | null;
  whatsapp_added_at: string | null;
  whatsapp_group_link?: string | null;
  final_grade: string | null;
  attendance_percentage: string | null;
  academic_approved: boolean;
  notes: string | null;
  student?: AuthUser;
  cohort?: CohortItem;
  whatsapp_added_by_user?: AuthUser;
  payments?: PaymentItem[];
  active_certificate?: CertificateItem | null;
  created_at: string;
}

export interface ComplaintItem {
  id: number;
  uuid: string;
  correlative_code: string;
  consumer_name: string;
  consumer_document_type: string;
  consumer_document_number: string;
  consumer_phone: string;
  consumer_email: string;
  consumer_address: string;
  item_type: 'product' | 'service';
  item_description: string | null;
  claimed_amount: string | null;
  complaint_type: 'claim' | 'complaint';
  detail: string;
  consumer_request: string;
  status: 'registered' | 'in_review' | 'responded' | 'closed';
  legal_deadline: string;
  company_response: string | null;
  responded_at: string | null;
  created_at: string;
}

export interface ContactLeadItem {
  id: number;
  uuid: string;
  full_name: string;
  email: string;
  phone: string;
  program_id: number | null;
  subject: string | null;
  message: string;
  source_path: string | null;
  status: 'new' | 'contacted' | 'qualified' | 'converted' | 'discarded';
  notes: string | null;
  program?: ProgramItem;
  created_at: string;
}

export interface AdvisorySlotItem {
  id: number;
  uuid: string;
  program_id: number;
  advisor_id: number;
  slot_date: string;
  start_time: string;
  end_time: string;
  modality: 'virtual' | 'presential';
  meeting_or_location: string | null;
  is_booked: boolean;
  student_id: number | null;
  topic: string | null;
  program?: ProgramItem;
  advisor?: AuthUser;
  student?: AuthUser;
}

export interface ActivityLogItem {
  id: number;
  user_id: number | null;
  action: string;
  subject_type: string;
  subject_id: string | null;
  properties: Record<string, unknown> | null;
  ip_address: string | null;
  user?: AuthUser;
  created_at: string;
}
