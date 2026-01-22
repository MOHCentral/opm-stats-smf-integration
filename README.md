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

1.  **Prerequisites**:
    -   SMF 2.1+ installed.
    -   Access to the OpenMOHAA Stats API (running separately).

2.  **Plugin Installation**:
    -   The core logic is located in `smf-plugins/mohaa_stats_core`.
    -   Copy the contents of `smf-plugins` to your SMF `Sources/` and `Themes/` directories as appropriate (detailed installer coming soon).

## Configuration

Navigate to **Admin** > **Configuration** > **MOHAA Stats** in your SMF Admin Panel (once installed) to configure:
-   **API Endpoint**: URL to your Go API (e.g., `http://localhost:8080`).
-   **API Key**: Secure token for communication.

## Development

If you are developing this plugin:
-   Edit files in `smf-plugins/`.
-   Use `smf-mohaa/` as a reference for core SMF files if needed.
