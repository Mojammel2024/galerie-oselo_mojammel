# Tests for Oselo Gallery Admin Panel

## Functional Tests
- **Add Artwork**: Added "Mona Lisa" by "Da Vinci", year 1503 → Appears in artworks table.
- **Edit Warehouse**: Changed "Stock A" to "Stock B" → Updated in warehouses table.
- **Delete Artwork**: Removed artwork ID 1 → No longer in table.
- **Assign Warehouse**: Linked artwork to warehouse → Warehouse name shows in table.

## Security Tests
- **XSS**: Entered `<script>alert('hack');</script>` in title → Shows as text, no alert.
- **SQL Injection**: Tried `' OR 1=1; --` in name field → Fails safely, no data leak.