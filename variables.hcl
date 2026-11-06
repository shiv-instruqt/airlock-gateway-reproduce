// The platform sets the instruqt_ system variables at launch and overwrites these
// defaults; they are declared only so that `instruqt lab validate` resolves them
// (same pattern as labs/mgw-teaser/variables.hcl).
variable "instruqt_session_id" {
  default     = "local"
  description = "Reserved: set by the platform"
}

variable "instruqt_sandbox_domain" {
  default     = "labs.instruqt.com"
  description = "Reserved: set by the platform"
}
