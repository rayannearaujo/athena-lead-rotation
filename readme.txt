# Athena Lead Rotation

A custom WordPress plugin that automatically distributes incoming leads among SDRs using a round-robin assignment system integrated with Fluent Forms and FluentCRM.

## Overview

Athena Lead Rotation was developed to automate lead assignment and ensure fair distribution among sales representatives (SDRs).

When a lead is submitted through Fluent Forms, the plugin automatically identifies the corresponding contact in FluentCRM and applies the SDR tag based on the next position in the rotation queue.

This eliminates manual lead assignment and guarantees balanced lead distribution across the sales team.

## Features

* Automatic lead distribution (Round-Robin)
* Fluent Forms integration
* FluentCRM integration
* Delayed processing using WP-Cron
* SDR rotation tracking
* Manual rotation reset
* WordPress admin dashboard
* Email-based contact matching
* Support for modern and legacy Fluent Forms hooks

## How It Works

1. A visitor submits a form through Fluent Forms.
2. The plugin schedules a background task.
3. The contact is located inside FluentCRM.
4. The next SDR in the rotation queue is selected.
5. The corresponding FluentCRM tag is applied.
6. The rotation index is updated for the next lead.

## Tech Stack

* PHP
* WordPress
* Fluent Forms
* FluentCRM
* WP-Cron

## Use Case

Designed for businesses that need automatic lead routing between multiple SDRs without requiring a CRM administrator to manually assign contacts.

## Project Highlights

* Custom WordPress plugin development
* CRM automation
* Lead routing logic
* Sales operations workflow automation
* Marketing technology integration

## Author

Rayanne Araújo

WordPress Developer | CRO Specialist | Automation Developer

GitHub: https://github.com/rayannearaujo
LinkedIn: https://linkedin.com/in/rayannearaujos

## License

GPL v2 or later
