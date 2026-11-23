# Evidence to collect

## Authentication
Capture the login screen and a protected URL redirect while logged out. Explain the redirect, then show that an issued staff account can sign in.

## Product management
Show a product with name, price, stock and its uploaded image. Capture Edit and the archived state using your test product.

## Customer CRUD
Show a customer with full name/email and no phone, then an edit and deletion confirmation. Use fictitious data.

## Staff and password security
Show staff creation/edit, avatar and role/access fields. Show a masked or redacted database password hash, never an actual password or private environment values.

## Successful sale
Show the selected product, optional customer, quantity and calculated total, then the success message. Note the product price and arithmetic used.

## Updated inventory
Show the same product stock before and after the sale. State the observed arithmetic: starting quantity minus sold quantity equals remaining stock.

## Insufficient stock
Attempt a quantity greater than remaining stock. Show the error and verify both stock and sales count are unchanged.

## Sales history
Show product, customer or Walk-in, recording staff, quantity, total price and date. Use a view wide enough to show every column.

## Access control
Show regular staff cannot manage staff accounts and a logged-out visitor cannot access management pages. Include logout behavior.

## Repository and deployment
Show your GitHub source and actual live deployment. Include your final commit and live URL. Hide credentials and private logs.

Use fictional customer details and hide credentials. Add actual results and your own reflection in documentation.html. Save filled HTML, then Check & print PDF. No screenshots or personal reflections were fabricated.
