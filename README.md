# OpenMOHAA Stats System - SMF Integration

This repository contains the Simple Machines Forum (SMF 2.1) integration for the OpenMOHAA Stats System.

## Overview

This integration allows your SMF forum to become the frontend for the stats system, providing:
- Player Profiles with rich stats (K/D, Accuracy, Weapons)
- Leaderboards
- Match History
- Server Dashboard

## Directory Structure

- `smf-plugins/`: The distributable plugins to install on your SMF forum.
- `smf-mohaa/`: (Development only) A local instance of SMF source code for development and testing.

## Installation

### 1. Build the Package
We provide a utility script to bundle the plugin into a format compatible with the SMF Package Manager.

1.  Run the build script from the project root:
    ```bash
    ./build_smf_package.sh
    ```
2.  This will create a file at `release/mohaa_stats_core_v1.0.0.zip`.

### 2. Install via SMF Admin Panel
1.  Log in to your SMF Forum as Administrator.
2.  Go to **Admin** > **Package Manager** > **Download Packages**.
3.  Scroll to the bottom to **Upload a Package**.
4.  Upload the `mohaa_stats_core_v1.0.0.zip` file.
5.  Click **Install Mod** and follow the prompts.
    *   This will automatically install the database tables and default configuration.

## Configuration

Navigate to **Admin** > **Configuration** > **MOHAA Stats** in your SMF Admin Panel (once installed) to configure:
-   **API Endpoint**: URL to your Go API (e.g., `http://localhost:8080`).
-   **API Key**: Secure token for communication.

## Development

If you are developing this plugin:
-   Edit files in `smf-plugins/`.
-   Use `smf-mohaa/` as a reference for core SMF files if needed.
