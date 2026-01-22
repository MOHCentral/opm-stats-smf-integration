# SMF MOHAA Core Integration

This directory contains the core integration files necessary to prepare your Simple Machines Forum (SMF) installation for the MOHAA-specific features. It includes database setup scripts, core file modifications, and custom themes.

**This is the foundation of the MOHAA integration and must be installed before any of the plugins in the `smf-plugins` directory.**

## Purpose

The files in this directory are responsible for:
-   Creating the necessary database tables and structures for the MOHAA stats system.
-   Integrating the MOHAA system with the SMF core by modifying `Sources` and `Themes` files.
-   Providing the base functionality that the individual feature plugins in `smf-plugins` will build upon.

## Installation

A detailed, step-by-step installation guide is available in the `smf-plugins` directory. This guide provides a comprehensive walkthrough of the entire setup process, from backing up your SMF installation to installing the individual plugins.

**Please refer to the main installation guide for detailed instructions:**

[**SMF MOHAA Integration and Plugins Installation Guide**](../smf-plugins/README.md)

### High-Level Installation Steps

1.  **Back up** your existing SMF installation and database.
2.  **Copy** the contents of this directory (`smf-mohaa/`) into your SMF root directory, merging the `Sources/` and `Themes/` directories.
3.  **Execute** the SQL scripts (`.sql` files) on your SMF database.
4.  **Run** any necessary PHP setup scripts (`.php` files).
5.  **Proceed** to install the individual feature plugins from the `smf-plugins` directory as described in the main guide.

By following the detailed instructions in the linked `README.md`, you will ensure a smooth and successful integration of the MOHAA stats system into your SMF forum.
