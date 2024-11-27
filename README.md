
# Map Assessment and Training System

This WordPress plugin allows administrators to create map-based assessments and for users to submit their answers by drawing routes on a map. Admins can then review submissions, provide feedback, and suggest corrections.

## Features

1. Admin can create questions with start (green) and end (red) markers on a map.
2. Users can draw routes between the provided markers to answer questions.
3. Admin can review user submissions, provide feedback, and suggest corrections by drawing on the map.
4. Users can view admin feedback and suggested routes.
5. Shortcode system for easy embedding of map assessments in posts or pages.

## Installation

1. Upload the `map-assessment` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to the 'Map Assessment' menu item in the WordPress admin panel to start creating questions.

## Usage

### For Administrators

1. Go to the 'Map Assessment' page in the WordPress admin panel.
2. Use the map interface to set green (start) and red (end) markers for your question.
3. Enter your question text and click 'Create Question'.
4. Copy the generated shortcode and paste it into any post or page where you want the map assessment to appear.
5. To review user submissions, click on the 'Results' button for a specific question.
6. View user submissions, provide feedback, and draw suggested routes on the map.

### For Users

1. Navigate to a page or post containing a map assessment.
2. Read the question and instructions carefully.
3. Use the 'Start Drawing' button to begin drawing your route on the map.
4. Use the 'Undo' button to remove the last point if you make a mistake.
5. Use the 'Clear' button to start over.
6. When you're satisfied with your route, click the 'Submit' button.
7. After submission, you can check for admin feedback and view any suggested routes.

## Shortcode

Use the following shortcode to embed a map assessment in a post or page:

```
[map_assessment id="X"]
```

Replace "X" with the ID of the question you want to display.

## Support

For support, please contact [Your Contact Information].

## License

This plugin is licensed under the GPL v2 or later.

```
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
```
