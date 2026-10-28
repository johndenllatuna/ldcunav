<?php

namespace Database\Seeders;

use App\Prisma\PrismaClient;
use Illuminate\Database\Seeder;

/**
 * LocationSeeder
 *
 * Populates campus locations for Liceo de Cagayan University.
 * Supports categories: Academic, Facilities, Services, Offices, Administrative.
 * Locations with mapOrder 1-4 correspond to the 4 prominent buildings in the UI map.
 * Safe to re-run (idempotent: checks for existence by unique slug).
 */
class LocationSeeder extends Seeder
{
    /**
     * @var list<array{name: string, slug: string, category: string, type: string, description: string, icon: string, mapOrder: int|null}>
     */
    public const LOCATIONS = [
        // ── ACADEMIC BUILDINGS ──────────────────────────────────────
        [
            'name' => 'North Academic Cluster',
            'slug' => 'north-academic-cluster',
            'category' => 'Academic',
            'type' => 'Academic Building',
            'description' => 'Home to the College of Arts and Sciences, general education classrooms, and department faculty rooms.',
            'icon' => '🏢',
            'mapOrder' => 1,
        ],
        [
            'name' => 'West Academic Cluster',
            'slug' => 'west-academic-cluster',
            'category' => 'Academic',
            'type' => 'Academic Building',
            'description' => 'Houses the College of Engineering, College of Architecture, and advanced computing laboratories.',
            'icon' => '🏛️',
            'mapOrder' => 2,
        ],
        [
            'name' => 'South Academic Cluster',
            'slug' => 'south-academic-cluster',
            'category' => 'Academic',
            'type' => 'Academic Building',
            'description' => 'Contains the College of Business and Accountancy, lecture theaters, and case analysis rooms.',
            'icon' => '🏫',
            'mapOrder' => null,
        ],
        [
            'name' => 'East Academic Cluster',
            'slug' => 'east-academic-cluster',
            'category' => 'Academic',
            'type' => 'Academic Building',
            'description' => 'Dedicated to Teacher Education, Criminology programs, and related seminar halls.',
            'icon' => '🎓',
            'mapOrder' => null,
        ],
        [
            'name' => 'Engineering Building',
            'slug' => 'engineering-building',
            'category' => 'Academic',
            'type' => 'Academic Building',
            'description' => 'Dedicated academic building for engineering programs, classrooms, and laboratories.',
            'icon' => 'building-arch',
            'mapOrder' => null,
        ],
        [
            'name' => 'College of Nursing & Allied Health',
            'slug' => 'college-of-nursing',
            'category' => 'Academic',
            'type' => 'Health Sciences Wing',
            'description' => 'Specialized skills laboratories, simulation hospital wards, and anatomy learning suites.',
            'icon' => '🩺',
            'mapOrder' => null,
        ],
        [
            'name' => 'College of Law & Graduate Studies',
            'slug' => 'college-of-law',
            'category' => 'Academic',
            'type' => 'Postgraduate Center',
            'description' => 'Houses the School of Law moot court room, graduate school seminar halls, and legal aid clinic.',
            'icon' => '⚖️',
            'mapOrder' => null,
        ],
        [
            'name' => 'Basic Education Complex',
            'slug' => 'basic-education-complex',
            'category' => 'Academic',
            'type' => 'Basic Education Building',
            'description' => 'Instructional building for junior and senior high school students with modern science laboratories.',
            'icon' => '🎒',
            'mapOrder' => null,
        ],

        // ── CAMPUS FACILITIES ───────────────────────────────────────
        [
            'name' => 'Rodolsa Hall',
            'slug' => 'rodolsa-hall',
            'category' => 'Facilities',
            'type' => 'University Facility',
            'description' => 'Multi-purpose auditorium hosting university convocations, academic symposia, and cultural performances.',
            'icon' => '🏢',
            'mapOrder' => 3,
        ],
        [
            'name' => 'Liceo Civic Center',
            'slug' => 'liceo-civic-center',
            'category' => 'Facilities',
            'type' => 'Campus Facility',
            'description' => 'Flagship university arena accommodating large assemblies, commencement exercises, and inter-collegiate events.',
            'icon' => '🏫',
            'mapOrder' => 4,
        ],
        [
            'name' => 'University Gymnasium & Sports Center',
            'slug' => 'university-gymnasium',
            'category' => 'Facilities',
            'type' => 'Sports Complex',
            'description' => 'Indoor sports facility with hardwood courts, student fitness gym, and varsity locker rooms.',
            'icon' => '🏀',
            'mapOrder' => null,
        ],
        [
            'name' => 'Campus Canteen',
            'slug' => 'university-cafeteria',
            'category' => 'Facilities',
            'type' => 'Dining Facility',
            'description' => 'Central campus dining area featuring diverse meal kiosks, student lounge seating, and snack bars.',
            'icon' => 'salad',
            'mapOrder' => null,
        ],
        [
            'name' => 'Campus Chapel',
            'slug' => 'university-chapel',
            'category' => 'Facilities',
            'type' => 'Spiritual Center',
            'description' => 'University chapel for Eucharistic celebrations, recollection activities, and personal prayer.',
            'icon' => 'building-church',
            'mapOrder' => null,
        ],

        // ── STUDENT & CAMPUS SERVICES ───────────────────────────────
        [
            'name' => 'University Library',
            'slug' => 'university-library',
            'category' => 'Services',
            'type' => 'Library Facility',
            'description' => 'Comprehensive learning commons providing print archives, digital journals, quiet reading zones, and study pods.',
            'icon' => '📚',
            'mapOrder' => null,
        ],
        [
            'name' => 'Health Services Clinic',
            'slug' => 'health-services-clinic',
            'category' => 'Services',
            'type' => 'Medical Facility',
            'description' => 'Outpatient health unit staffed by licensed physicians and nurses offering first aid, consultations, and medical clearance.',
            'icon' => '🏥',
            'mapOrder' => null,
        ],
        [
            'name' => 'IT Services Lab & Network Center',
            'slug' => 'it-services-lab',
            'category' => 'Services',
            'type' => 'Technology Facility',
            'description' => 'Main computer learning center, campus Wi-Fi support desk, and institutional IT helpdesk.',
            'icon' => '🖥️',
            'mapOrder' => null,
        ],
        [
            'name' => 'Guidance & Psychological Services',
            'slug' => 'guidance-center',
            'category' => 'Services',
            'type' => 'Student Support',
            'description' => 'Provides student counseling, career guidance, psychological testing, and student wellness programs.',
            'icon' => '🧠',
            'mapOrder' => null,
        ],
        [
            'name' => 'Campus Safety & Security Office',
            'slug' => 'campus-security-office',
            'category' => 'Services',
            'type' => 'Campus Security',
            'description' => 'Campus entry verification, lost-and-found repository, emergency response unit, and vehicle pass station.',
            'icon' => '🛡️',
            'mapOrder' => null,
        ],

        // ── STUDENT OFFICES ─────────────────────────────────────────
        [
            'name' => "Registrar's Office",
            'slug' => 'registrars-office',
            'category' => 'Offices',
            'type' => 'Administrative Office',
            'description' => 'Administers student enrollment records, transcript of records requests, grade certifications, and graduation evaluation.',
            'icon' => '🗂️',
            'mapOrder' => null,
        ],
        [
            'name' => "Cashier's & Finance Office",
            'slug' => 'cashiers-office',
            'category' => 'Offices',
            'type' => 'Financial Services',
            'description' => 'Assists students with tuition fee settlements, statement of accounts, payment plans, and scholarship disbursements.',
            'icon' => '💰',
            'mapOrder' => null,
        ],
        [
            'name' => 'Office of Student Affairs & Services',
            'slug' => 'office-of-student-affairs',
            'category' => 'Offices',
            'type' => 'Student Affairs',
            'description' => 'Supervises student organizations, campus activities, student publication, and community leadership programs.',
            'icon' => '📋',
            'mapOrder' => null,
        ],
        [
            'name' => 'Admissions & Information Desk',
            'slug' => 'admissions-office',
            'category' => 'Offices',
            'type' => 'Student Admissions',
            'description' => 'Frontline desk for university inquiries, freshman admissions processing, campus tours, and enrollment guidelines.',
            'icon' => 'ℹ️',
            'mapOrder' => null,
        ],

        // ── ADMINISTRATIVE OFFICES ──────────────────────────────────
        [
            'name' => 'Human Resource Management Office (HRMO)',
            'slug' => 'hrmo',
            'category' => 'Administrative',
            'type' => 'Administrative Office',
            'description' => 'Manages university faculty and staff records, professional development, and employee services.',
            'icon' => '👥',
            'mapOrder' => null,
        ],
        [
            'name' => 'Office of the University President',
            'slug' => 'office-of-the-president',
            'category' => 'Administrative',
            'type' => 'Executive Office',
            'description' => 'Executive governance office directing strategic initiatives, university policy, and institutional partnerships.',
            'icon' => '🏛️',
            'mapOrder' => null,
        ],
        [
            'name' => 'University Research & Publication Center',
            'slug' => 'research-and-publications',
            'category' => 'Administrative',
            'type' => 'Research Center',
            'description' => 'Coordinates institutional research ethics, faculty publications, student research symposiums, and peer-reviewed journals.',
            'icon' => '🔬',
            'mapOrder' => null,
        ],
    ];

    public function run(PrismaClient $prisma): void
    {
        $created = 0;
        $updated = 0;

        foreach (self::LOCATIONS as $location) {
            $existing = $prisma->location->findUnique([
                'where' => ['slug' => $location['slug']],
            ]);

            if ($existing === null) {
                $prisma->location->create(['data' => $location]);
                $created++;
            } else {
                $prisma->location->updateMany([
                    'where' => ['id' => (int) $existing['id']],
                    'data' => [
                        'name' => $location['name'],
                        'category' => $location['category'],
                        'type' => $location['type'],
                        'description' => $location['description'],
                        'icon' => $location['icon'],
                        'mapOrder' => $location['mapOrder'],
                    ],
                ]);
                $updated++;
            }
        }

        $this->command?->info("LocationSeeder: {$created} created, {$updated} updated (total: " . count(self::LOCATIONS) . ').');
    }
}
