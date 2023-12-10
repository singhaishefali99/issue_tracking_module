# Issue Tracking Custom Module

Overview
--------

This Drupal module provides functionality for tracking and managing 
issues within the Drupal site.It includes a block that displays 
the latest assigned issues for the current user.
It provides support for Drupal 9/10 only.

Features
--------

- Latest Assigned Issues Block: Displays the three 
  latest issues assigned to the current user.
- Issue Content Type: Utilizes a custom content type 'issue' to 
  store information about individual issues.

Installation
------------

1. Download and place the module in your Drupal modules directory 
   (e.g., modules/custom/issue_tracking).
   
2. Enable the module using Drush or through the Drupal administrative interface.

   ```drush
   drush en custom_issue_tracking
