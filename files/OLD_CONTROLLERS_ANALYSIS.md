# Old Controllers Analysis

## Summary
This document identifies all controllers that are still using the OLD form management system instead of the NEW unified form system.

## Old Form System vs New Form System

### NEW System (Current Standard)
- **Models**: `Form`, `FormSubmission`, `FormField`, `FormSection`
- **Tables**: `forms`, `form_submissions`, `form_fields`, `form_sections`
- **Controllers**: `Admin/FormController`, `Admin/SubmissionController`, `Public/FormController`, `Public/FormSubmissionController`

### OLD System (Legacy - Still Exists)
- **Models**: 
  - `RemittanceApplicationForm`, `DataAccessRequestForm`, `DataCorrectionRequestForm`, `ServiceRequestForm`
  - `RafFormSubmission`, `DarFormSubmission`, `DcrFormSubmission`, `SrfFormSubmission`
  - `RafFormField`, `DarFormField`, `DcrFormField`, `SrfFormField`
- **Tables**: `remittance_application_forms`, `data_access_request_forms`, etc.
- **Controllers**: All controllers in `Raf/`, `Dar/`, `Dcr/`, `Srf/` directories

## Old Controllers Still Using Old System

### Form Controllers (4)
1. ✅ **Raf/RemittanceApplicationFormController.php** - Uses `RemittanceApplicationForm`
2. ✅ **Dar/DataAccessRequestFormController.php** - Uses `DataAccessRequestForm`
3. ✅ **Dcr/DataCorrectionRequestFormController.php** - Uses `DataCorrectionRequestForm`
4. ✅ **Srf/ServiceRequestFormController.php** - Uses `ServiceRequestForm`

### Submission Controllers (4)
1. ✅ **Raf/RafFormSubmissionController.php** - Uses `RafFormSubmission`
2. ✅ **Dar/DarFormSubmissionController.php** - Uses `DarFormSubmission`
3. ✅ **Dcr/DcrFormSubmissionController.php** - Uses `DcrFormSubmission`
4. ✅ **Srf/SrfFormSubmissionController.php** - Uses `SrfFormSubmission`

### FormField Controllers (4)
1. ✅ **Raf/RafFormFieldController.php** - Uses `raf_form_id`
2. ✅ **Dar/DarFormFieldController.php** - Uses `dar_form_id`
3. ✅ **Dcr/DcrFormFieldController.php** - Uses `dcr_form_id`
4. ✅ **Srf/SrfFormFieldController.php** - Uses `srf_form_id`

### Related Controllers (5)
1. ✅ **Dar/DarResponseDataController.php** - Related to DAR submissions
2. ✅ **Dcr/DcrCorrectionActionController.php** - Related to DCR submissions
3. ✅ **Dcr/DcrVerificationRecordController.php** - Related to DCR submissions
4. ✅ **Srf/SrfServiceActionController.php** - Related to SRF submissions
5. ✅ **Srf/SrfServiceHistoryController.php** - Related to SRF submissions

## Total Old Controllers: 17

## Status
These controllers are **STILL ACTIVE** and may be:
1. **Legacy controllers** - Kept for backward compatibility
2. **Admin-specific controllers** - For managing old form data
3. **Specialized controllers** - For handling form-specific workflows (like service actions, corrections, etc.)

## Recommendation
- If these are legacy and no longer needed → **Deprecate/Remove**
- If these are still in use → **Keep but document as legacy**
- If these handle specialized workflows → **Keep but consider migration to new system**

## Migration Path
If migrating to new system:
1. Map old form types to new Form model using `settings['type']`
2. Migrate data from old tables to new tables
3. Update routes to use new controllers
4. Update views to use new form structure

