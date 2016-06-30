# CsvDiff

Just a simple php script that compares two csv data files and outputs the difference to a separate file.

The script assumes that both CSV files are built in spreadsheet style and contain header cells. It matches the given cell name for both given files (both csv must contain same unique cell name) and compares the contents of the files and outputs all the rows that exists only in the second one to a separate file.

I write the script to compare translation dumps: I had a set of old translation data in one csv and the current translation data in other and I needed to find out which rows were new and not translated. But this can be used to compare any two csv-files.

## Usage

    php csv_diff.php old_data.csv new_curent_data.csv ID

This assumes that the content of both files contain "ID" cell om the first row. The position of the cell in each file however can be different.

Example:

    ID;LOREM;IPSUM
    23;Lorem;Ipsum
    24;Lalem;Musip

Output of the above example:

    new_curent_data.csv.diff.csv

The contents of the file follows the contents of new_curent_data.csv
