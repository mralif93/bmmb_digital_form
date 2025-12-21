# Guide: Creating Checkbox Fields with Terms & Conditions

## Overview

This guide explains how to create checkbox fields that include detailed terms and conditions text below the checkbox, similar to the "Conversion of Qard account to Tawarruq account" example.

## Field Structure

For a checkbox with terms/conditions, you need:
1. **Field Type**: `checkbox` (single checkbox, no options)
2. **Field Label**: The checkbox label (e.g., "6. Conversion of Qard account to Tawarruq account / Pertukaran dari akaun Qard ke akaun Tawarruq")
3. **Help Text / Description**: The detailed terms and conditions text

## Step-by-Step Instructions

### In the Form Builder:

1. **Click "Create Field"**

2. **Fill in Basic Information:**
   - **Section**: Select the appropriate section
   - **Field Name**: `qard_to_tawarruq_conversion` (or similar)
   - **Field Label**: `6. Conversion of Qard account to Tawarruq account / Pertukaran dari akaun Qard ke akaun Tawarruq`
   - **Field Type**: Select `Checkbox`
   - **Column Position**: `Full Width` (recommended for long text)

3. **Configure Checkbox:**
   - **Required Field**: Check this if the user must agree
   - **Active**: Check this
   - **Do NOT add any options** - Leave the options field empty (this creates a single checkbox)

4. **Add Terms & Conditions in Help Text:**
   - In the **Help Text** field, paste your terms and conditions
   - Use line breaks (`Enter`) to separate paragraphs
   - Example format:
   ```
   Customer / Pelanggan:
   I hereby authorize Bank Muamalat Malaysia Berhad (BMMB) to act as my agent (Wakalah) to utilize funds from my Qard account...

   Bank / Bank:
   The Bank agrees to accept the appointment as agent...
   ```

5. **Save the Field**

## Example Field Configuration

```
Field Name: qard_to_tawarruq_conversion
Field Label: 6. Conversion of Qard account to Tawarruq account / Pertukaran dari akaun Qard ke akaun Tawarruq
Field Type: checkbox
Required: Yes
Active: Yes
Column Position: Full Width
Help Text:
Customer / Pelanggan:
I hereby authorize Bank Muamalat Malaysia Berhad (BMMB) to act as my agent (Wakalah) to utilize funds from my Qard account to purchase Shariah-compliant commodities at a purchase price and thereafter sell the same commodities to me at a Murabahah Sale Price, whereby the proceeds from the sale of the commodities shall be credited into my Tawarruq account and shall be governed by the terms and conditions of the Tawarruq account.

Bank / Bank:
The Bank agrees to accept the appointment as agent and shall perform its obligations and protect the Customer's interest in good faith. The Customer's Qard account funds shall be transferred to a Tawarruq account and shall be governed by the terms and conditions of the Tawarruq account.
```

## How It Renders

The field will render as:
- ✅ Checkbox aligned in the middle with the label
- Label text next to the checkbox
- Detailed terms/conditions text displayed below the checkbox and label
- Multi-line text is properly formatted with line breaks preserved

## Tips

1. **Use Full Width**: For long terms text, set Column Position to "Full Width" for better readability
2. **Line Breaks**: Press `Enter` in the Help Text field to create new paragraphs
3. **Bilingual Text**: You can include both English and Malay text in the Help Text field
4. **Required Field**: If this is a consent/agreement checkbox, mark it as required so users must check it to proceed

## Conditional Logic

You can use this checkbox as a trigger for conditional logic:
- If checked, show additional fields related to the conversion
- Example: Show "Tawarruq Account Details" fields when this checkbox is checked

## Notes

- The Help Text field supports multi-line text
- Line breaks are automatically converted to `<br>` tags for proper display
- The text appears below the checkbox in a readable format
- Text color is gray-700 for good readability

